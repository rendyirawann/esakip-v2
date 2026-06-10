<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipSasaranrenstraP;

/**
 * SakipSasaranrenstraPSearch represents the model behind the search form of `frontend\models\SakipSasaranrenstraP`.
 */
class SakipSasaranrenstraPSearch extends SakipSasaranrenstraP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsasaranrenstra_p_id', 'refsasaranrenstra_id', 'refskpd_id', 'refsasaran_p_id', 'refvisi_p_id', 'refmisi_p_id', 'reftujuan_p_id', 'refperiode_5tahun_id', 'reftujuanrenstra_p_id'], 'integer'],
            [['uraian_sasaranrenstra_p', 'sasaranrenstra_p_isaktif', 'alasan_sasaranrenstra_p', 'formulasi_sasaranrenstra_p', 'kriteria_sasaranrenstra_p'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipSasaranrenstraP::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refTujuan', 'refSasaran', 'refVisi', 'refMisi']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refsasaranrenstra_p_id' => $this->refsasaranrenstra_p_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refsasaran_p_id' => $this->refsasaran_p_id,
            'refvisi_p_id' => $this->refvisi_p_id,
            'refmisi_p_id' => $this->refmisi_p_id,
            'reftujuan_p_id' => $this->reftujuan_p_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'reftujuanrenstra_p_id' => $this->reftujuanrenstra_p_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaranrenstra_p', $this->uraian_sasaranrenstra_p])
            ->andFilterWhere(['like', 'sasaranrenstra_p_isaktif', $this->sasaranrenstra_p_isaktif])
            ->andFilterWhere(['like', 'alasan_sasaranrenstra_p', $this->alasan_sasaranrenstra_p])
            ->andFilterWhere(['like', 'formulasi_sasaranrenstra_p', $this->formulasi_sasaranrenstra_p])
            ->andFilterWhere(['like', 'kriteria_sasaranrenstra_p', $this->kriteria_sasaranrenstra_p]);

        return $dataProvider;
    }
}
