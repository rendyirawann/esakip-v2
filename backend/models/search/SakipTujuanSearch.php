<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipTujuan;

/**
 * SakipTujuanSearch represents the model behind the search form of `backend\models\SakipTujuan`.
 */
class SakipTujuanSearch extends SakipTujuan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reftujuan_id', 'refvisi_id', 'refmisi_id', 'refperiode_5tahun_id'], 'integer'],
            [['uraian_tujuan', 'indikator_tujuan', 'tujuan_isaktif'], 'safe'],
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
        $query = SakipTujuan::find();

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
            'reftujuan_id' => $this->reftujuan_id,
            'refvisi_id' => $this->refvisi_id,
            'refmisi_id' => $this->refmisi_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_tujuan', $this->uraian_tujuan])
            ->andFilterWhere(['like', 'indikator_tujuan', $this->indikator_tujuan])
            ->andFilterWhere(['like', 'tujuan_isaktif', $this->tujuan_isaktif]);

        return $dataProvider;
    }
}
