<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipSasaranrenstra;

/**
 * SakipSasaranrenstraSearch represents the model behind the search form of `frontend\models\SakipSasaranrenstra`.
 */
class SakipSasaranrenstraSearch extends SakipSasaranrenstra
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsasaranrenstra_id', 'refskpd_id', 'refperiode_5tahun_id', 'refvisi_id', 'refmisi_id', 'refsasaran_id', 'reftujuanrenstra_id', 'reftujuan_id'], 'integer'],
            [['uraian_sasaranrenstra', 'sasaranrenstra_isaktif', 'alasan_sasaranrenstra', 'formulasi_sasaranrenstra', 'kriteria_sasaranrenstra'], 'safe'],
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
        $query = SakipSasaranrenstra::find();

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
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refvisi_id' => $this->refvisi_id,
            'refmisi_id' => $this->refmisi_id,
            'reftujuan_id' => $this->reftujuan_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'refsasaran_id' => $this->refsasaran_id,
            'reftujuanrenstra_id' => $this->reftujuanrenstra_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaranrenstra', $this->uraian_sasaranrenstra])
            ->andFilterWhere(['like', 'alasan_sasaranrenstra', $this->alasan_sasaranrenstra])
            ->andFilterWhere(['like', 'sasaranrenstra_isaktif', $this->sasaranrenstra_isaktif])
            ->andFilterWhere(['like', 'formulasi_sasaranrenstra', $this->formulasi_sasaranrenstra])
            ->andFilterWhere(['like', 'kriteria_sasaranrenstra', $this->kriteria_sasaranrenstra]);

        return $dataProvider;
    }
}
