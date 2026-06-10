<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipTujuanP;

/**
 * SakipTujuanSearch represents the model behind the search form of `backend\models\SakipTujuan`.
 */
class SakipTujuanPSearch extends SakipTujuanP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reftujuan_p_id', 'refvisi_p_id', 'refmisi_p_id', 'refperiode_5tahun_id'], 'integer'],
            [['uraian_tujuan_p', 'indikator_tujuan_p', 'tujuan_p_isaktif'], 'safe'],
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
        $query = SakipTujuanP::find();

        // add conditions that should always apply here
        $query->with(['visi', 'misi', 'periode']);

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
            'reftujuan_p_id' => $this->reftujuan_p_id,
            'refvisi_p_id' => $this->refvisi_p_id,
            'refmisi_p_id' => $this->refmisi_p_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_tujuan_p', $this->uraian_tujuan_p])
            ->andFilterWhere(['like', 'indikator_tujuan_p', $this->indikator_tujuan_p])
            ->andFilterWhere(['like', 'tujuan_p_isaktif', $this->tujuan_p_isaktif]);

        return $dataProvider;
    }
}
